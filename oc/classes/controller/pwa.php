<?php

class Controller_pwa extends Kohana_Controller {

    public function action_manifest()
    {
        $this->response->headers('Content-Type',  'application/json');
        $this->response->body(View::factory('pwa/manifest'));
    }

    public function action_service_worker()
    {
        $this->response->headers('Content-Type',  'text/javascript');
        $this->response->body(View::factory('pwa/service_worker'));
    }

    public function action_offline()
    {
        $this->response->headers('Content-Type',  'text/html');
        $this->response->body(View::factory('pwa/offline'));
    }
}
