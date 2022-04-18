import {createApp} from 'vue'

const app = createApp({
    //el: '#app',
    delimiters:['${', '}$'],
    data: () => ({
        message: 'String from Vue.js'
    })
})

app.mount('#app')

console.log(app);