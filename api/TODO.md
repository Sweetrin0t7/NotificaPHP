Em relação à API:

--------------------------A FAZER:

- Deve utilizar Orientação a Objetos;

- Deve implementar apenas dois tipos de rotas, como por exemplo:
  /api/clientes para todo o conjunto de clientes
  /api/clientes/123 para um cliente específico

- Deve utilizar a reescrita de URLs e fazer o roteamento adequado;

- Deve validar dados e tratar erros, devolvendo a resposta adequada:
  Tratar rotas inexistes e métodos não permitidos;
  Validar as requisições recebidas (no controller);
  Validar as regras de negócio (no service);

- Deve responder sempre em JSON, com o código de status HTTP adequado;

- Deve implementar rotas com GET, POST, PUT, PATCH e DELETE;
  Para as rotas que retornam conjuntos de registros, a requisição GET deve permitir a filtragem dos dados;
  No caso de PUT, POST e PATCH, os dados devem ser enviados no corpo da requisição em formato JSON;

--------------------------FEITO:

- Deve implementar o padrão Controller – Service – Repository; (Renata)

- Deve utilizar PDO para a interação com o banco de dados; (Renata/Alessandra)

- Deve implementar uma rota /api/db que cria a(s) tabela(s) e um conjunto de dados de
  exemplo no banco de dados. (Renata)

- Deve implementar a rota / que retorna uma apresentação da API, com a identificação
  do(s) autor(es) e a lista de todas as rotas (caminho e método) disponíveis, também em
  formato JSON: { “autores”: [...], “rotas”: [...]; (Renata)

--------FEITO EM USUÁRIO:

--------FEITO EM DENÚNCIA:

- Deve validar dados e tratar erros, devolvendo a resposta adequada:
  Tratar rotas inexistes e métodos não permitidos; (Renata - Denuncia)
  Validar as requisições recebidas (no controller); (Renata - Denuncia)
  Validar as regras de negócio (no service); (Renata - Denuncia)

- Deve implementar apenas dois tipos de rotas, como por exemplo:
  /api/clientes para todo o conjunto de clientes (Renata - Denuncia)

- Deve responder sempre em JSON, com o código de status HTTP adequado; (Renata - Denuncia)

- Deve implementar rotas com GET, POST, PUT, PATCH e DELETE; (Renata - Denuncia)
  Para as rotas que retornam conjuntos de registros, a requisição GET deve permitir a filtragem dos dados; (Renata - Denuncia)
  No caso de PUT, POST e PATCH, os dados devem ser enviados no corpo da requisição em formato JSON; (Renata - Denuncia)
