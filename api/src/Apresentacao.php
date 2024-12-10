<?php

namespace Api;  

class Apresentacao {

    public static function apresentacao() {
        $response = [
            'autores' => ['Autor 1', 'Autor 2'],
            'rotas' => [
                'GET /api/denuncias',  
                'POST /api/denuncias', 
                'GET /api/denuncia/:id',  
                'DELETE /api/denuncia/:id', 
                'GET /api/courses/:id/students', 
                'GET /api/db',
                'GET /api' 
            ]
        ];

        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
