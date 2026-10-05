import Vue from 'vue'
import {
  BootstrapVue,
  BIconBoxArrowRight,
  BIconChatQuote,
  BIconCheckCircleFill,
  BIconExclamationTriangleFill,
  BIconInfoCircle,
  BIconPencilSquare,
  BIconStars,
} from 'bootstrap-vue'

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import './styles.css'

import App from './App.vue'

Vue.use(BootstrapVue)

Object.entries({
  BIconBoxArrowRight,
  BIconChatQuote,
  BIconCheckCircleFill,
  BIconExclamationTriangleFill,
  BIconInfoCircle,
  BIconPencilSquare,
  BIconStars,
}).forEach(([name, component]) => Vue.component(name, component))

Vue.config.productionTip = false

new Vue({
  render: (h) => h(App),
}).$mount('#app')
