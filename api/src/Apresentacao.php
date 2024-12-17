<?php

namespace Api;  

class Apresentacao {

    public static function apresentacao() {
        $response = [
            'autores' => ['Alessandra Freitas Pacheco', 'Renata Oliveira Schäfer'],
            'rotas' => [
                'GET /api/denuncias',  //Listagem
                'POST /api/denuncias', //Criar
                'GET /api/denuncia/:id',  //Listar ID
                'POST /api/denuncias/:id', //Editar ID
                'DELETE /api/denuncia/:id',
                'GET /api/denuncias?status={status}', //Filtrar status
                'GET /api/denuncias?titulo={titulo}', //Filtrar titulo
                'GET /api/usuarios', //Listagem
                'POST /api/usuarios', //Criar
                'GET /api/usuarios/:id',// Listar ID
                'PUT /api/usuarios/:id', //Editar ID
                'DELETE /api/usuarios/:id', //Delete ID
                'GET /api/usuarios?nome_usuario={nome_usuario}', //Filtrar nome
                'GET /api/usuarios?cpf_usuario={cpf_usuario}', //Filtrar cpf
                'GET /api/usuarios?telefone={telefone}', //Filtrar telefone
                'GET /api/db',
                'GET /api' 
            ]
        ];

        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
