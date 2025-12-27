import { ref, computed, provide, inject } from 'vue'

const SidebarSymbol = Symbol()

export function useSidebarProvider() {
  const isExpanded = ref(true)
  const isHovered = ref(false)
  const activeItem = ref(null)


  const toggleSidebar = () => {
    isExpanded.value = !isExpanded.value
  }

  const setIsHovered = (value) => {
    isHovered.value = value
  }

  const setActiveItem = (item) => {
    activeItem.value = item
  }

  const context = {
    isExpanded: computed(() => (isExpanded.value)),
    isHovered,
    activeItem,
    toggleSidebar,
    setIsHovered,
    setActiveItem,
  }

  provide(SidebarSymbol, context)

  return context
}

export function useSidebar() {
  const context = inject(SidebarSymbol)
  if (!context) {
    throw new Error(
      'useSidebar must be used within a component that has SidebarProvider as an ancestor',
    )
  }
  return context
}
