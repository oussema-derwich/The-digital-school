<template>
    <footer class="footer">
    <span class="todo-count"><strong>{{ nbTodoDone }}</strong> item{{ (nbTodoDone>1) ? 's' : ''}} left</span>
    <ul class="filters">
    <li>
    <a :class="{selected: filter == '_All_'}" href="#/" @click="$emit('on-filter', '_All_')">All</a>
    </li>
    <li>
    <a :class="{selected: filter == '_Active_'}" href="#/active" @click="$emit('on-filter', '_Active_')">Active</a>
    </li>
    <li>
    <a :class="{selected: filter == '_Completed_'}" href="#/completed" @click="$emit('on-filter', '_Completed_')">Completed</a>
    </li>
    </ul>
    <button class="clear-completed" @click="$emit('on-clear-complete')">Clear completed ({{ nbTodoNotDone }})</button>
    </footer>
    </template>
    
    <script>
    export default {
      name: 'TodoInfo',
    props: {
        todos: {
          type: Array,
          default: ()=> []
        },
    filter: {
    type: String
    }
      },
    emits: ['on-filter', 'on-clear-complete'],
    computed: {
    nbTodoDone(){
    return this.todos.reduce((total, todo) => {
    if(!todo.done){
    total++;
    }
    return total;
    }, 0);
    },
    nbTodoNotDone(){
    return this.todos.length - this.nbTodoDone
    }
    }
    }
    </script>
    
    <style>
     
    </style>
    