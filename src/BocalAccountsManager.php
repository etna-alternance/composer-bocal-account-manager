<?php

namespace ETNA\Silex\Provider\BocalAccounts;

use GuzzleHttp\Client;

class BocalAccountsManager
{
    private $api_key;
    private $guzzle_client;
    private $read_only;

    public function __construct($api_endpoint, $api_key, $read_only = false)
    {
        $this->read_only     = $read_only;
        $this->api_key       = $api_key;
        $this->guzzle_client = new Client([
            "base_uri" => $api_endpoint
        ]);
    }

    /**
     * Retourne la liste des comptes bocal
     *
     * @return array
     */
    public function listAccounts()
    {
        return $this->request("GET", "/api/etna.io/users", [
            'X-Fields' => 'id,firstname,lastname,uuid,group,synchronized,external_email'
        ]);
    }

    /**
     * Retourne les informations d'un compte bien précis
     *
     * @param  string $account_id ID du compte bocal
     *
     * @return array
     */
    public function getAccount($account_id)
    {
        return $this->request("GET", "/api/etna.io/users/{$account_id}", [
            'X-Fields' => 'id,firstname,lastname,uuid,group,synchronized,external_email'
        ]);
    }

    /**
     * Retourne le numéro de téléphone lié au compte indiqué
     *
     * @param  string $account_id ID du compte bocal
     *
     * @return array
     */
    public function getAccountTelephone($account_id)
    {
        return $this->request("GET", "/api/etna.io/users/{$account_id}/tel");
    }

    /**
     * Effectue la création d'un compte
     *
     * @param  string $firstname  Prénom
     * @param  string $lastname   Nom
     * @param  string $group      Type du compte (students|teachers|administratives)
     * @param  string $email_etna Email pour l'envoi des IDs
     *
     * @return array
     */
    public function createAccount($firstname, $lastname, $group, $email_etna)
    {
        $body = [
            "firstname"      => $firstname,
            "lastname"       => $lastname,
            "group"          => $group,
            "external_email" => $email_etna,
            "send_mail"      => "True"
        ];


        if ($this->read_only) {
            return $body;
        }

        return $this->request("POST", "/api/etna.io/users", [
            'X-Fields' => 'id,firstname,lastname,uuid,group,synchronized,external_email'
        ], $body);
    }

    /**
     * Met à jour les informations d'un compte donné
     *
     * @param  string $account_id   ID du compte bocal
     * @param  array  $account_data Les informations que l'on veut mettre à jour
     *
     * @return array
     */
    public function updateAccount($account_id, $account_data)
    {
        if ($this->read_only) {
            return $account_data;
        }

        return $this->request("POST", "/api/etna.io/users/{$account_id}", [
            'X-Fields' => 'id,firstname,lastname,uuid,group,synchronized,external_email'
        ], $account_data);
    }

    /**
     * Fonction envoyant une requête HTTP
     *
     * @param  string     $method             Méthode HTTP de la requête
     * @param  string     $uri                URI de la requête
     * @param  array      $additional_headers Si on veut ajouter des headers
     * @param  array|null $body               Les données du body pour les POST|PUT
     *
     * @return array
     */
    private function request($method, $uri, $additional_headers = [], $body = null)
    {
        $headers = array_merge([
            "Authorization" => "Bearer {$this->api_key}"
        ], $additional_headers);

        try {
            $response = $this->guzzle_client->request($method, $uri, [
                "headers" => $headers,
                "json"    => $body
            ]);
            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\RequestException $client_error) {
            throw new \Exception(
                $client_error->getResponse()->getReasonPhrase(),
                $client_error->getResponse()->getStatusCode()
            );
        }
    }
}
