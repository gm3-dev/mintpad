const Closable = {
    mounted (el, binding, vnode) {
        el.clickOutsideEvent = (e) => {
            e.stopPropagation()
            
            const { handler, exclude } = binding.value
            const element = document.getElementById(exclude)
            let clickedOnExcludedEl = element.contains(e.target)
            if (!el.contains(e.target) && !clickedOnExcludedEl) {
                handler()
            }
        }

        document.addEventListener('click', el.clickOutsideEvent)
        document.addEventListener('touchstart', el.clickOutsideEvent)
    },
    unmounted (el) {
        document.removeEventListener('click', el.clickOutsideEvent)
        document.removeEventListener('touchstart', el.clickOutsideEvent)
    }
}
  
export default Closable