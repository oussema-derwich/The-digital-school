&lt;template>
        <template>
          <div class="bg-white rounded-md shadow-card p-6">
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <slot name="icon"></slot>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-secondary">
                  <slot name="title"></slot>
                </h3>
                <div class="mt-2 text-sm text-secondary/70">
                  <slot></slot>
                </div>
              </div>
            </div>
          </div>
        </template>

        <script setup lang="ts">
        // Props et logique à ajouter selon besoins
        </script>