<template>
  <input
    type="text"
    :placeholder="placeholder"
    :value="modelValue"
    @input="inputValue"
    class="border border-bitchest-black rounded-2xl p-2 mb-4 w-full md:w-auto"
  />
</template>

<script>
export default {
  name: "CustomInput",
  props: {
    placeholder: {
      type: String,
      default: "Your Text Here",
    },
    modelValue: {
      // Utilisation correcte pour v-model dans Vue 3
      type: String,
      default: "",
    },
  },
  methods: {
    inputValue(event) {
      this.$emit("update:modelValue", event.target.value);
    },
  },
};
</script>
