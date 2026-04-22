

# 🛠 Decisões Técnicas e Arquitetura do Projeto


## 1. Justificativas das Decisões Técnicas

### Autenticação e Segurança
* **Laravel Sanctum:** Escolhido por ser a solução oficial e leve do ecossistema Laravel para APIs. Garante uma integração nativa e segura sem a complexidade desnecessária do Passport para este escopo.
* **Rate Limiting:** Implementado o limite de **20 requisições por minuto** para garantir a estabilidade do serviço e prevenir abusos nos endpoints da API.

### Estrutura de Dados e Performance
* **Paginação Standard:** Utilização do método `paginate()`. Diferente da paginação simples, esta fornece metadados completos (total de registos, última página, etc.), essenciais para uma experiência de utilizador fluida no Frontend.
* **Cálculo Dinâmico (Anti-Redundância):** O valor total dos contratos é calculado em tempo real a partir dos itens. Esta decisão evita a redundância de dados e elimina riscos de desincronização financeira.
* **Snapshots de Valor:** Embora o contrato seja dinâmico, a `Billing` (Cobrança) armazena o valor no momento da geração. Isso garante a integridade histórica caso os preços dos itens do contrato mudem no futuro.

### Arquitetura e Manutenibilidade
* **Observers para Auditoria:** Implementados para monitorar mudanças de status em `ServiceOrder` e `Billing`. Esta abordagem isola a lógica de registo de histórico, mantendo os Models e Controllers limpos e focados nas suas responsabilidades principais.
* **PHP Enums:** Utilizados para padronizar os estados das entidades. Garante integridade ao nível da aplicação, facilita o refactoring e evita erros por "magic strings".
* **Soft Deletes:** Aplicado em Clientes e Contratos. Em sistemas de gestão, a preservação de dados para auditoria é crítica; a exclusão lógica permite rastreabilidade mesmo após a remoção pelo utilizador.

---

## 📈 Status de Implementação (To-Do List)

### Parte 1: Modelagem e Estrutura Base
- [x] **Configuração do Ambiente:** Laravel 12, PHP 8.2+ e MySQL.
- [x] **Migrations:** Estrutura completa (Clientes, Contratos, Itens, Cobranças, OS, Histórico).
- [x] **Relacionamentos Eloquent:** Configuração de todas as chaves estrangeiras e tabelas pivot.
- [x] **Cálculo Dinâmico:** Soma de itens de contrato sem persistência redundante.
- [x] **Soft Deletes:** Implementação em entidades críticas.

### Parte 2: Regras de Negócio e Enums
- [x] **Enums de Status:** Padronização para Cobranças e Ordens de Serviço.
- [x] **Auditoria Automática:** Uso de Observers para registo de histórico de mudanças.
- [x] **ACL (Spatie):** Gestão de roles `financial` e `employee`.

### Parte 3: API e Segurança
- [x] **Autenticação:** Proteção de rotas com Laravel Sanctum.
- [x] **Rate Limiting:** Restrição de 20 chamadas/min.
- [x] **Form Requests:** Validação de dados centralizada.
- [ ] **Dashboard:** Endpoints de filtros por status e inadimplência. *(Pendente)*

### Parte 4: Processamento Assíncrono (Jobs)
- [ ] **Job `ApplyPendingCredit`:** Lógica de abatimento automático de saldo.
- [ ] **Idempotência:** Implementação de *Atomic Locks* via Cache.
- [ ] **Resiliência:** Configuração de Retry com *Exponential Backoff*.

---

## 🏁 Entrega e Demonstração
- [x] **Factories & Seeders:** Para popular o banco de dados.
- [ ] **Postman Collection:** Exportação com exemplos de uso.
- [ ] **Vídeo de Demonstração:** Explicação técnica e execução dos processos.

