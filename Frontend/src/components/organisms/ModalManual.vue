<script setup>
import ManualStep from '@/components/molecules/ManualStep.vue'
defineProps({
  Title: {
    type: String,
    required: true,
  },
})
</script>

<template>
    <div class="flex flex-col items-start">
            <h3 class="mb-2 text-lg font-semibold text-colors">{{ Title }}</h3>

            <ManualStep
                step="1"
                title="Request the book"
                description="The current owner will be notified of your interest and can approve or decline your request."
              />

              <ManualStep
                step="2"
                title="Owner Accepts"
                description="As soon as it's approved, pay the postage fee and track your shipment. If both sides agree, a physical meet can be arranged, instead of delivery."
              />

              <ManualStep
                step="3"
                title="Track delivery"
                description="Follow the shipment status and estimated delivery date. Mark the book as received once it arrives."
              />
            
          </div>
</template>