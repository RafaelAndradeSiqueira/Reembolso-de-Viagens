<template>
  <div class="calendario">
    <div class="calendario-grade calendario-cabecalho">
      <div v-for="titulo in titulos" :key="titulo">{{ titulo }}</div>
    </div>

    <div class="calendario-grade">
      <div v-for="n in espacosIniciais" :key="`vazio-${n}`" />

      <button
        v-for="celula in celulas"
        :key="celula.data"
        type="button"
        class="calendario-dia"
        :class="{ 'fim-de-semana': celula.fimDeSemana, folga: celula.excluida }"
        :disabled="celula.fimDeSemana"
        :title="dicaDaCelula(celula)"
        @click="$emit('alternar', celula.data)"
      >
        <span class="numero-dia">{{ celula.dia }}</span>
        <span v-if="!celula.fimDeSemana" class="valor-dia dinheiro">
          <template v-if="celula.excluida">folga</template>
          <template v-else>
            <span class="d-none d-sm-inline">{{ formatarDinheiro(celula.valor) }}</span>
            <span class="d-sm-none">{{ formatarNumeroCurto(celula.valor) }}</span>
          </template>
        </span>
      </button>
    </div>
  </div>
</template>

<script>
import { diaDaSemana, formatarDinheiro, formatarNumeroCurto, paraDataIso, totalDeDiasNoMes } from '../formatacao'

export default {
  name: 'CalendarioMes',

  props: {
    ano: { type: Number, required: true },
    mes: { type: Number, required: true },
    valores: { type: Object, required: true },
    datasExcluidas: { type: Array, default: () => [] },
  },

  data() {
    return { titulos: ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'] }
  },

  computed: {
    espacosIniciais() {
      return diaDaSemana(this.ano, this.mes, 1) - 1
    },

    celulas() {
      const celulas = []

      for (let dia = 1; dia <= totalDeDiasNoMes(this.ano, this.mes); dia++) {
        const diaSemana = diaDaSemana(this.ano, this.mes, dia)
        const data = paraDataIso(this.ano, this.mes, dia)
        celulas.push({
          dia,
          data,
          fimDeSemana: diaSemana > 5,
          excluida: this.datasExcluidas.includes(data),
          valor: diaSemana <= 5 ? this.valores[diaSemana] : 0,
        })
      }
      return celulas
    },
  },

  methods: {
    formatarDinheiro,
    formatarNumeroCurto,

    dicaDaCelula(celula) {
      if (celula.fimDeSemana) return 'Fim de semana'
      return celula.excluida ? 'Clique para voltar a contar' : 'Clique para marcar como folga/feriado'
    },
  },
}
</script>

<style scoped>
.calendario-grade {
  display: grid;
  grid-template-columns: repeat(7, minmax(0, 1fr));
  gap: 6px;
}

.calendario-cabecalho {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  color: #8a7f86;
  text-align: center;
  margin-bottom: 6px;
}

.calendario-dia {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: space-between;
  min-height: 64px;
  padding: 6px 8px;
  border: 1px solid #ecdfe4;
  border-radius: 0.5rem;
  background: #fff;
  text-align: left;
  transition: transform 0.08s ease, background 0.15s ease;
}

.calendario-dia:not(:disabled):hover {
  transform: translateY(-1px);
  border-color: var(--marca);
}

.calendario-dia:focus {
  outline: 2px solid var(--marca);
  outline-offset: 1px;
}

.numero-dia {
  font-weight: 600;
}

.valor-dia {
  font-size: 0.75rem;
  color: var(--marca-escura);
  background: var(--marca-suave);
  border-radius: 1rem;
  padding: 0 6px;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.calendario-dia.fim-de-semana {
  background: transparent;
  border-style: dashed;
  color: #c4bcc0;
  cursor: default;
}

.calendario-dia.folga {
  background: #f1eeee;
  color: #9a9094;
}

.calendario-dia.folga .numero-dia {
  text-decoration: line-through;
}

.calendario-dia.folga .valor-dia {
  background: #e3dedf;
  color: #7b7276;
}

@media (max-width: 575px) {
  .calendario-grade {
    gap: 3px;
  }

  .calendario-dia {
    min-height: 52px;
    padding: 4px;
  }

  .valor-dia {
    font-size: 0.65rem;
    padding: 0 4px;
  }
}
</style>
