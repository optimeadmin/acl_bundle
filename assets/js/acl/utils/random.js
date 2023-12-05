export function getRandomNumber(min, max) {
  return min + (Math.random() * (max - min))
}

export function getRandomInt(min, max) {
  return Math.floor(getRandomNumber(min, max))
}