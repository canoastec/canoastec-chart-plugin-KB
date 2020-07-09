CanoastecChart
==============================

Requirements
------------

- Kanboard >= 1.2.13

Documentation
-------------

- Vá até a pasta plugins do projeto kanboard
- Rode o comando para baixar o plugin

``` bash
git clone https://github.com/canoastec/canoastec-chart-plugin-KB.git

```

- Após baixar renomeie a pasta do plugin para CanoastecChart.
- Entre na pasta e execute

``` bash
composer install
```

``` bash
npm install
```

``` bash
npm run dev
```

- Renomeie o arquivo .env.example para .env, dentro deste arquivo possui a seguinte estrutura:

``` bash
PROJECT_ID="ID DO PROJECT KANBOARD"
COLUMN="COLUMN DO PROJECT KANBOARD"
```

- Substitua o **"ID DO PROJECT KANBOARD"**, pelo o id do projeto da onde quer tirar as informações.

- Substitua o **"COLUMN DO PROJECT KANBOARD"**, pela query de busca de tarefas do kanboard. Exemplo:
  
``` bash 
COLUMN="column:Andamento" 
```

- E execute o kanboard normalmente, o gráfico gerado pelo plugin pode ser visto na página inicial do kanboard, no menu lateral, menu **Grafico estimado x executado**

