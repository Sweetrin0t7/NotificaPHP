# NotificaPHP - Backend2

## Estrutura de Pastas e Arquivos

### /api
A pasta `/api` contém a implementação dos controladores, serviços e repositórios da API.

#### /controller
Os controladores são responsáveis por gerenciar as requisições HTTP e interagir com os serviços.

- **UsuarioController.php**: Controlador para gerenciar as requisições relacionadas aos usuários.
- **DenunciaController.php**: Controlador para gerenciar as requisições relacionadas às denúncias.

#### /service
A pasta `/service` contém a lógica de negócio, que manipula as regras principais do sistema.

- **UsuarioService.php**: Serviço que contém a lógica de manipulação dos dados do usuário.
- **DenunciaService.php**: Serviço que contém a lógica de manipulação das denúncias.

#### /repository
Os repositórios são responsáveis pela interação com o banco de dados, fornecendo métodos para recuperar e persistir dados.

- **UsuarioRepository.php**: Repositório para manipular dados dos usuários no banco de dados.
- **DenunciaRepository.php**: Repositório para manipular dados das denúncias no banco de dados.

#### /model
Os modelos representam as entidades principais do sistema e sua estrutura de dados.

- **Usuario.php**: Modelo para representar um usuário no sistema.
- **Denuncia.php**: Modelo para representar uma denúncia no sistema.

#### /database
Contém arquivos relacionados à configuração e manipulação do banco de dados.

- **Database.php**: Classe responsável pela configuração da conexão com o banco de dados.

#### /http
Contém arquivos para lidar com as requisições HTTP.

- **request.php**: Arquivo que gerencia as requisições HTTP recebidas.

#### /error
Arquivos relacionados ao tratamento de erros e exceções na API.

- **APIException.php**: Classe que define exceções personalizadas para a API.

### /index.php
O ponto de entrada da aplicação, onde a execução do sistema começa.

### /TODO.md
Arquivo de acompanhamento de tarefas e planejamento do projeto.

### /.htaccess
Arquivo de configuração do servidor web (geralmente Apache), para gerenciamento de URLs e segurança.

---
