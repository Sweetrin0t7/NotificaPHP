Este é um exemplo de uso do padrão Contoller-Service-Repository para a construção de uma API Rest usando PHP puro. Para fins didáticos, alguns pontos foram simplificados ou abstraídos. Da mesma forma, algumas soluções foram adotadas a fim de permitir a problematização de aspectos importantes do projeto durante as aulas, além da discussão das mudanças necessárias para a sua evolução ou para a implementação de outras técnias/princípios.

Entidades e Relações
- Curso (nome e número de períodos);
- Estudante (matrícula, nome, email, curso e período no curso);
- Um estudante deve estar matriculado em um único curso.

Requisitos
- Deve ser possível criar, alterar e excluir um curso;
- Deve ser possível obter os dados de um curso pelo id;
- Deve ser possível obter uma listagem de cursos;
- Deve ser possível filtrar os cursos pelo nome;
- Deve ser possível criar, alterar e excluir um estudante;
- Deve ser possível obter os dados de um estudante pelo id;
- Deve ser possível obter uma listagem de estudantes;
- Deve ser possível filtrar os estudante pelo nome;
- Deve ser possível atualizar o período do estudante;
- O estudante é identificado por uma matrícula que deve ser gerada automaticamente pelo sistema;

Regras de negócio
- O id do curso não pode ser alterado;
- O nome de curso deve ter pelo menos 5 caracteres;
- O número de períodos do curso deve ser maior do que zero;
- Não deve ser possível excluir um curso que possua estudantes matriculados;
- A metrícula de um estudante não pode ser alterada;
- O nome de um estudante deve ter pelo menos 5 caracteres;
- O estudante deve ter um email válido;
- Dois estudantes não podem ter o mesmo email;
- O estudante deve estar matriculado em um curso existente;
- O periodo do estudante deve ser maior do que zero;
- O período do estudante deve ser menor ou igual ao número de períodos do curso em que está matriculado.