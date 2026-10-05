<template>
  <div id="app">
    <b-navbar v-if="usuario" type="dark" class="barra-marca mb-4">
      <b-navbar-brand class="font-weight-bold">🚗 Reembolso de Viagens</b-navbar-brand>
      <b-navbar-nav class="ml-auto flex-row align-items-center">
        <b-button v-if="valores" size="sm" variant="light" class="mr-2" @click="$bvModal.show('modal-valores')">
          <b-icon-pencil-square /> <span class="d-none d-sm-inline">Valores por dia</span>
        </b-button>
        <b-button size="sm" variant="outline-light" @click="sair">
          <b-icon-box-arrow-right /> <span class="d-none d-sm-inline">Sair</span>
        </b-button>
      </b-navbar-nav>
    </b-navbar>

    <b-container class="pb-5">
      <b-row v-if="!usuario" class="justify-content-center align-items-center tela-acesso">
        <b-col sm="10" md="6" lg="4">
          <div class="text-center mb-4">
            <div class="display-4">🚗</div>
            <h3 class="font-weight-bold mb-0">Reembolso de Viagens</h3>
            <small class="text-muted">Quanto a empresa te deve este mês</small>
          </div>

          <formulario-cadastro v-if="mostrarCadastro" @entrou="aoEntrar" @voltar="mostrarCadastro = false" />
          <formulario-entrar v-else @entrou="aoEntrar" @criar-conta="mostrarCadastro = true" />
        </b-col>
      </b-row>

      <div v-else-if="carregando" class="text-center py-5">
        <b-spinner class="text-marca" />
      </div>

      <b-alert v-else-if="erroCarregamento" show variant="danger">
        {{ erroCarregamento }}
        <b-button size="sm" variant="outline-danger" class="ml-2" @click="carregarValores">Tentar de novo</b-button>
      </b-alert>

      <b-row v-else-if="!valores" class="justify-content-center">
        <b-col md="8" lg="6">
          <b-card>
            <h4 class="mb-1">Oi, {{ primeiroNome }}! 👋</h4>
            <p class="text-muted">
              Antes de começar, diga quanto a empresa paga em cada dia da semana.
              Você pode mudar isso depois.
            </p>
            <formulario-valores texto-botao="Salvar e continuar" @salvou="valores = $event" />
          </b-card>
        </b-col>
      </b-row>

      <painel-reembolso v-else :valores="valores" />
    </b-container>

    <b-modal id="modal-valores" title="Valores por dia da semana" hide-footer>
      <formulario-valores :valores="valores" @salvou="aoSalvarValores" />
    </b-modal>
  </div>
</template>

<script>
import FormularioEntrar from './components/FormularioEntrar.vue'
import FormularioCadastro from './components/FormularioCadastro.vue'
import FormularioValores from './components/FormularioValores.vue'
import PainelReembolso from './components/PainelReembolso.vue'
import autenticacaoService from './services/autenticacaoService'
import valoresService from './services/valoresService'
import { definirAoExpirarSessao, mensagemDeErro } from './services/api'

export default {
  name: 'App',
  components: { FormularioEntrar, FormularioCadastro, FormularioValores, PainelReembolso },

  data() {
    return {
      usuario: autenticacaoService.usuarioAtual(),
      valores: null,
      mostrarCadastro: false,
      carregando: false,
      erroCarregamento: null,
    }
  },

  computed: {
    primeiroNome() {
      return this.usuario ? this.usuario.nome.split(' ')[0] : ''
    },
  },

  created() {
    definirAoExpirarSessao(() => this.sair())
    if (this.usuario) {
      this.carregarValores()
    }
  },

  methods: {
    aoEntrar(usuario) {
      this.usuario = usuario
      this.mostrarCadastro = false
      this.carregarValores()
    },

    async carregarValores() {
      this.carregando = true
      this.erroCarregamento = null
      try {
        this.valores = await valoresService.buscar()
      } catch (erro) {
        if (this.usuario) {
          this.erroCarregamento = mensagemDeErro(erro)
        }
      } finally {
        this.carregando = false
      }
    },

    aoSalvarValores(valores) {
      this.valores = valores
      this.$bvModal.hide('modal-valores')
      this.$bvToast.toast('Valores atualizados!', { variant: 'success', solid: true, autoHideDelay: 2500 })
    },

    sair() {
      autenticacaoService.sair()
      this.usuario = null
      this.valores = null
    },
  },
}
</script>
