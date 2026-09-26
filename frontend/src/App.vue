<template>
  <nav>
    <RouterLink to="/">
      Home
    </RouterLink>
    <RouterLink to="/inventory">
      Inventory
    </RouterLink>
  </nav>

  <main>
    <RouterView />
  </main>
</template>
