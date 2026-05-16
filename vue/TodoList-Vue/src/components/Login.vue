<template>
    <div class="login-container">
      <h2>Login</h2>
      <form @submit.prevent="login">
        <input type="text" v-model="username" placeholder="Username" required />
        <input type="password" v-model="password" placeholder="Password" required />
        <button type="submit">Login</button>
      </form>
    </div>
  </template>
  
  <script>
  export default {
    name: 'FormLogin',
    data() {
      return {
        username: '',
        password: ''
      };
    },
    methods: {
      login() {
        // Replace with real authentication logic
        if (this.username && this.password) {
          this.$router.push('/todos');
        } else {
          alert('Please enter valid credentials');
        }
      }
    }
  };
  </script>
  
  <style>
  .login-container {
    max-width: 300px;
    margin: 100px auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    text-align: center;
  }
  .login-container input {
    display: block;
    width: 100%;
    margin-bottom: 10px;
    padding: 10px;
    box-sizing: border-box;
  }
  .login-container button {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  .login-container button:hover {
    background-color: #45a049;
  }
  </style>
  