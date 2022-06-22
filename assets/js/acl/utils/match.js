export const matchOrX = (content, searchBy) => {
    const parts = searchBy.trim().split('|').filter(p => p.length > 2)
    const expression = new RegExp('(' + parts.join(')|(') + ')', 'g')

    return content.match(expression)
}