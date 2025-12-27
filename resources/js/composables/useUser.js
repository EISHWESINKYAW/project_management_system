function setUser(newUser) {
  localStorage.setItem('user', JSON.stringify(newUser));
}

export function useUser() {
  return {
    setUser
  };
}
