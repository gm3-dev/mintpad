if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    var useDarkmode = true
    document.documentElement.classList.add('dark')
} else {
    var useDarkmode = false
    document.documentElement.classList.remove('dark')
}