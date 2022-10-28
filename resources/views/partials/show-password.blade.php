<span class="text-mintpad-300 text-center show-password-container">
    <a v-if="!showPassword" href="#" @click.prevent="toggleShowPassword(true)"><i class="fas fa-eye-slash"></i></a>
    <a v-else href="#" @click.prevent="toggleShowPassword(false)"><i class="fas fa-eye"></i></a>
</span>