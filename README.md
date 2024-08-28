# Guia de Instalação do Projeto Laravel com Vue.js - Marcasite Cursos 

+ Este guia descreve o processo de instalação de um projeto Laravel com Vue.js. Siga os passos abaixo para configurar o ambiente e rodar o projeto.

+ O projeto consiste em um sistema de cadastro de cursos, onde é possível adicionar, editar e excluir cursos. O frontend foi desenvolvido com Vue.js e o backend com Laravel.

## 1. Clonar o Repositório

Primeiro, clone o repositório do projeto usando o Git:

```bash
git clone https://github.com/GuilhermeTiede/teste_marcasite_cursos.git
```

## 2. Instalar Dependências do Laravel
Navegue até o diretório do projeto e instale as dependências do Laravel com o Composer:

```bash Copiar código
cd teste_marcasite_cursos
composer install
```
## 3. Instalar Dependências do Frontend
Em seguida, instale as dependências do frontend usando o Yarn:

```bash Copiar código
yarn install
```
## 4. (Opcional) Configuração com Laravel Valet
Se você utiliza o Laravel Valet para gerenciar seu ambiente de desenvolvimento, execute os seguintes comandos para criar o link simbólico e habilitar o HTTPS:

```bash Copiar código
valet link
valet secure
```
Depois, acesse o link do repositório gerado pelo Valet no seu navegador.

## 5. Configuração do Arquivo .env
Edite o arquivo .env na raiz do projeto para configurar a conexão com seu banco de dados
MySQL. Certifique-se de definir as seguintes variáveis de ambiente:

```makefile Copiar código
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```
## 6. Executar as Migrações
Após configurar o banco de dados, execute as migrações para criar as tabelas necessárias:

```bash Copiar código
php artisan migrate
```

## 7. Rodar o Projeto
Para iniciar o servidor de desenvolvimento e rodar o projeto, utilize o comando abaixo:

```bash Copiar código
yarn dev
```
Se preferir, você pode apenas gerar os arquivos de build para produção usando o Vite:

```bash Copiar código
yarn build
```
## 8. Acessar o Projeto
Se você configurou o Laravel Valet, acesse o projeto pelo link fornecido. Caso contrário, o projeto estará acessível através do servidor embutido do Laravel na URL padrão (http://localhost:8000).

Pronto! ambiente configurado e o projeto deve estar rodando corretamente.
Se algum imprevisto surgir não exitem em me chamar que estarei disposto a ajudar.

