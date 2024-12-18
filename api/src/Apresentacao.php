<?php

namespace Api;  

class Apresentacao {

    public static function apresentacao() {
        $response = [
            'autores' => ['Alessandra Freitas Pacheco', 'Renata Oliveira Schafer'],
            'rotas' => [
                'GET /api/denuncias',  //Listagem
                'POST /api/denuncias', //Criar
                'GET /api/denuncias/:id',  //Listar ID
                'POST /api/denuncias/:id', //Editar ID
                'DELETE /api/denuncias/:id',
                'GET /api/denuncias?status={status}', //Filtrar status
                'GET /api/denuncias?titulo={titulo}', //Filtrar titulo
                'GET /api/usuarios', //Listagem
                'POST /api/usuarios', //Criar
                'GET /api/usuarios/:id',// Listar ID
                'PUT /api/usuarios/:id', //Editar ID
                'DELETE /api/usuarios/:id', //Delete ID
                'GET /api/db',
                'GET /api' 
            ]
        ];

        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
