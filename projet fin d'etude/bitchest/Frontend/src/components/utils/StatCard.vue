<template>
  <div class="stat-card text-bitchest-black">
    <div class="stat-card__header">
      <div :class="`p-3 rounded-full bg-${color}-100 text-${color}-600`">
        <i :class="`fas fa-${icon} text-2xl`"></i>
      </div>
      <h3 class="stat-card__title">{{ title }}</h3>
    </div>

    <div class="stat-card__values">
      <div class="stat-card__value">
        <span>{{ formatValue(value) }} {{ unit }}</span>
      </div>
      <span
        v-if="percentage !== 'NA' && percentage !== null && percentage !== ''"
        class="stat-card__percentage"
        :class="percentage >= 0 ? 'text-green-600' : 'text-red-600'"
      >
        ({{ formatValue(percentage) }} %)
      </span>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true,
    },
    value: {
      type: [Number, String],
      required: true,
    },
    unit: {
      type: String,
      default: "",
    },
    icon: {
      type: String,
      default: "",
    },
    color: {
      type: String,
      default: "blue",
    },
    percentage: {
      type: [Number, String],
      default: "NA",
    },
  },
  methods: {
    formatValue(value) {
      return new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      })
        .format(value)
        .replace(/\s/g, " ");
    },
  },
};
</script>

<style scoped>
.stat-card {
  background: white;
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-card__header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.stat-card__title {
  font-size: 18px;
  font-weight: bold;
}

.stat-card__values {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 24px;
  font-weight: bold;
}

.stat-card__value {
  color: v-bind(color);
}

.stat-card__percentage {
  font-size: 18px;
  font-weight: bold;
}
</style>
