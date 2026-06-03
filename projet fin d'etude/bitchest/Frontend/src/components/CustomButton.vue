<template>
  <button :class="[buttonClass, $attrs.class]" >
    
    <slot></slot>
  </button>
</template>

<script>
export default {
  name: "CustomButton",
  props: {
    variant: {
      type: String,
      default: "primary",
    },
  },
  computed: {
    buttonClass() {
      return {
        "bg-bitchest-success font-bitchest font-bold text-bitchest-black px-4 py-2 w-122 h-42 rounded-2xl hover:bg-bitchest-secondary":
          this.variant === "primary",
      };
    },
  },
};
</script>
