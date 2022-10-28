<span class="text-mintpad-300 text-center show-password-container">
    <a v-if="!showConfirmPassword" href="#" @click.prevent="toggleShowConfirmPassword(true)"><i class="fas fa-eye-slash"></i></a>
    <a v-else href="#" @click.prevent="toggleShowConfirmPassword(false)"><i class="fas fa-eye"></i></a>
</span>