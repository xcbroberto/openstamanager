<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SetupController extends Controller
{
    /**
     * Verifica la connessione al database secondo i parametri indicati nella richiesta.
     * Restituisce un array di permessi mancanti in caso la connessione avvenga con successo, oppure null in caso contrario.
     */
    final public function testDatabase(Request $request): Response|JsonResponse
    {
        $database_name = $request->input('database_name');

        // Configurazione della connessione di test
        config(['database.connections.testing' => [
            'driver' => 'mysql',
            'host' => $request->input('host'),
            'port' => '3306',
            'password' => $request->input('password'),
            'database' => $database_name,
            'username' => $request->input('username'),
        ]]);

        try {
            $connection = DB::connection('testing');

            // Controlla se la connessione al DB è stata stabilita in due modi
            $connection->getPdo();
            if (empty($connection->getDatabaseName())) {
                throw new Exception(__('Impossibile connettersi al database selezionato! Controllare il nome del database'));
            }

            // Individuazione permessi garantiti all'utente
            $database_name = Str::replace('_', '\_', $database_name);
            $grants = $connection->select($connection->raw('SHOW GRANTS FOR CURRENT_USER'));
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $requirements = [
            'SELECT',
            'INSERT',
            'UPDATE',
            'CREATE',
            'ALTER',
            'DROP',
        ];

        foreach ($grants as $result) {
            $privileges = current($result);

            if (Str::contains($privileges, [" ON `$database_name`.*", ' ON *.*'])) {
                $pieces = explode(', ', explode(' ON ', str_replace('GRANT ', '', $privileges), 2)[0]);

                // Permessi generici sul database
                if (in_array('ALL', $pieces) || in_array('ALL PRIVILEGES', $pieces)) {
                    $requirements = [];
                    break;
                }

                // Permessi specifici sul database
                foreach ($requirements as $key => $value) {
                    if (in_array($value, $pieces)) {
                        unset($requirements[$key]);
                    }
                }
            }
        }

        if (count($requirements) === 0) {
            return response()->noContent();
        }

        return response()->json([
            'error' => __("L'utente del database non ha i seguenti permessi necessari: ", $requirements),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Metodo indirizzato al salvataggio della configurazione.
     */
    public function save(Request $request)
    {
        $text = '<?php return '.var_export(config('database'), true).';';
        $result = File::put(config_path('database.php'), $text);

        // Errore in caso di fallimento
        if ($result === false) {
            $chmodded = File::chmod(config_path('database.php'), 0644);
            $result = File::put(config_path('database.php'), $text);
            if ($result === false) {
                return response()->json([
                    'error' => 'writing',
                    'error_description' => __('Impossibile scrivere il file di configurazione. :action', ['action' => !$chmodded ? 'Controllare i permessi del file config/databasee.php' : '']),
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        // Refresh della cache sulla configurazione
        Artisan::call('cache:clear');
        Artisan::call('config:cache');

        setting($request->only(['timestamp_format', 'date_format', 'time_format', 'locale']));

        return response()->noContent();
    }
}