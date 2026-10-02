## Why

A fila incremental obriga a classificar o card atual para conhecer outro item. Isso impede a pessoa de consultar ou comparar cards já servidos e de seguir para o próximo antes de decidir sua classificação.

## What Changes

- A sessão de revisão passa a manter a posição do card atualmente exibido entre os cards já servidos.
- A API permite avançar para o próximo card elegível sem criar uma revisão e retornar aos cards já servidos na mesma sessão.
- A interface expõe controles Anterior e Próximo, desabilitados quando não houver card correspondente.

## Capabilities

### New Capabilities

- Nenhuma.

### Modified Capabilities

- `continuous-review-queue`: permite navegar entre cards servidos e servir o próximo sem classificação, preservando a seleção dinâmica e o histórico imutável.

## Impact

- Contrato HTTP e documentação de Review.
- Sessão, repositórios Doctrine, casos de uso e testes de Review.
- Módulo DDD e tela Quasar de revisão no frontend.
