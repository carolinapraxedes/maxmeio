# Sistema de Gestão de Agência Digital - API

## Tecnologias Utilizadas

- **PHP 8.2+**
- **Laravel 12**
- **MySQL 8.0**
- **Laravel Sanctum** (Autenticação)
- **Spatie Laravel Permission** (Controle de acesso ACL)

---

## Instalação e Configuração Local

Siga os passos abaixo para colocar o projeto em funcionamento:

### 1. Clonar o Repositório

```
git clone [https://github.com/carolinapraxedes/maxmeio.git](https://github.com/carolinapraxedes/maxmeio.git)
```
Em seguida:

```cd seu-repositorio```

### 2. Instalar Dependências

``` composer install ```

### 3. Configuração do Ambiente
Crie o arquivo ```.env```  a partir do exemplo:

```cp .env.example .env ```

Configure as credenciais do seu banco de dados no arquivo ```.env```


```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha
```


### 3. Gerar Chave da Aplicação e Migrations

```
php artisan key:generate

php artisan migrate --seed 
```

## Documentação da API

A documentação detalhada dos endpoints  está disponível na Postman Collection incluída na raiz do projeto:

### Credenciais de Teste (Padrão do Seeder)
```
{
    "email": "financeiro@agencia.com",
    "password": "password"
}
```

## Decisões Técnicas

As justificativas detalhadas sobre a arquitetura, escolha de padrões de projeto (Design Patterns) e resolução de ambiguidades do enunciado podem ser encontradas no arquivo dedicado: DECISOES.md
