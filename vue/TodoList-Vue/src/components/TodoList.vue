<template>
    <ul class="todo-list">
      <Todo v-for="(todo, index) in todosFiltered" :title="todo.title" :done="todo.done" :index="index" :edited="edited" :key="index" @on-toggle="$emit('on-toggle', $event)" @on-remove="$emit('on-remove', $event)" @end-editing="$emit('end-editing', $event)" @start-editing="$emit('start-editing', $event)" />
  </ul>
  </template>
  
  <script>
  import Todo from './TodoApp.vue';
  
  export default {
    name: 'TodoList',
    components: { Todo },
    props: {
      todos: {
        type: Array,
        default: ()=> []
      },
      filter:{
        type: String
      },
      edited: {
        type: Number
      }
    },
    computed: {
      todosFiltered(){
        return this.todos.filter(todo=>{
          switch(this.filter){
            case '_Active_':
              return todo.done == false;
            case '_Completed_':
              return todo.done == true;
            default:
              return true;
          }
        });
      }
    }
  }
  </script>
  
  <style>
  
  </style>