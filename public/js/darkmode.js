if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark')
    var useDarkmode = true
} else {
    document.documentElement.classList.remove('dark')
    var useDarkmode = false
}