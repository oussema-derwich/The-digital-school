<template>
    <li :class="{completed: done, view: !done, editing: edited == index}">
      <div class="view">
        <input class="toggle" type="checkbox" @click="$emit('on-toggle', index)" :checked="done" />
        <label @dblclick="startEditing">{{ title }}</label>
        <button class="destroy" @click="$emit('on-remove', index)"></button>
      </div>
      <input ref="inputEdit" class="edit" v-model="titleEdited" @keydown.enter="endEditing" />
    </li>
  </template>
  
  <script>
  export default {
    name: 'TodoApp',
    props: {
      title: {
        type: String,
        required: true
      },
      done: {
        type: Boolean,
        required: true
      },
      index: {
        type: Number
      },
      edited: {
        type: Number
      }
    },
    emits: ['on-toggle', 'on-remove', 'start-editing', 'end-editing'],
    data(){
      return {
        titleEdited: this.title
      }
    },
    methods: {
      startEditing(){
        setTimeout(()=>{
          this.$refs.inputEdit.focus();
        }, 5);
        this.$emit('start-editing', this.index);
      },
      endEditing(){
        this.$emit('end-editing', {id: this.index, title: this.titleEdited});
      }
    }
  }
  </script>
  
  <style>
  
  </style>