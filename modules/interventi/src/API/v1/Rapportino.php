<?php

namespace Modules\Interventi\API\v1;

use API\Interfaces\CreateInterface;
use API\Interfaces\DeleteInterface;
use API\Interfaces\RetrieveInterface;
use API\Interfaces\UpdateInterface;
use API\Resource;
use Modules;
use Modules\Anagrafiche\Anagrafica;
use Modules\Emails\Template;

class Rapportino extends Resource implements RetrieveInterface, CreateInterface
{
    public function retrieve($request)
    {
        $database = database();
        $id_record = $request['id_intervento'];

        $template = Template::where('name', 'Rapportino intervento')->first();
        $module = $template->module;

        $body = $template['body'];
        $subject = $template['subject'];

        $body = $module->replacePlaceholders($id_record, $template['body']);
        $subject = $module->replacePlaceholders($id_record, $template['subject']);
        $email = $module->replacePlaceholders($id_record, '{email}');

        $prints = $database->fetchArray('SELECT id, title, EXISTS(SELECT id_print FROM em_print_template WHERE id_template = '.prepare($template['id']).' AND em_print_template.id_print = zz_prints.id) AS selected FROM zz_prints WHERE id_module = '.prepare($module->id).' AND enabled = 1');

        return [
            'email' => $email,
            'subject' => $subject,
            'body' => $body,
            'prints' => $prints,
        ];
    }

    public function create($request)
    {
        // TODO: Implement create() method.
    }
}
