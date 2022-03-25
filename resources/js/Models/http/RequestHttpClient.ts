import {RequestHttpClientPromise} from '@osm/Models/http/RequestHttpClientPromise';
import {Request} from '@osm/utils';
import type {
  HttpClient,
  HttpClientPromise
} from 'coloquent';

/**
 * @class RequestHttpClient
 *
 * NOTE: This class is not meant to be used directly, but only for Models.
 * You should use the {@link Request} class instead.
 */
export class RequestHttpClient implements HttpClient {
  request: Request;

  constructor(requestInstance?: Request) {
    this.request = requestInstance ?? new Request({
      headers: {
        Accept: 'application/vnd.api+json',
        'Content-Type': 'application/vnd.api+json'
      }
    });
  }

  delete(url: string): HttpClientPromise {
    this.request.options.method = 'delete';
    this.request.options.url = url;
    return new RequestHttpClientPromise(this.request.send());
  }

  get(url: string): HttpClientPromise {
    this.request.options.method = 'get';
    this.request.options.url = url;
    return new RequestHttpClientPromise(this.request.send());
  }

  getImplementingClient() {
    return this.request;
  }

  head(url: string): HttpClientPromise {
    this.request.options.method = 'head';
    this.request.options.url = url;
    return new RequestHttpClientPromise(this.request.send());
  }

  patch(url: string, data?: any): HttpClientPromise {
    this.request.options.method = 'patch';
    this.request.options.url = url;
    return new RequestHttpClientPromise(this.request.send());
  }

  post(url: string, data?: any): HttpClientPromise {
    this.request.options.method = 'post';
    this.request.options.url = url;
    return new RequestHttpClientPromise(this.request.send());
  }

  put(url: string, data?: any): HttpClientPromise {
    this.request.options.method = 'put';
    this.request.options.url = url;
    return new RequestHttpClientPromise(this.request.send());
  }

  setWithCredentials(withCredientials: boolean): void {
    this.request.options.withCredentials = withCredientials;
  }
}
