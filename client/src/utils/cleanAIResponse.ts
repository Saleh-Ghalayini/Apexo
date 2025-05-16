// Utility to clean up AI responses for display as plain text
export function cleanAIResponse(text: string): string {
  return text
    .replace(/#+\s?/g, '') // Remove Markdown headings (###, ##, #)
    .replace(/\*\*/g, '')  // Remove bold
    .replace(/^- /gm, '')   // Remove list dashes at line start
    .replace(/`/g, '')      // Remove backticks
    .replace(/---+/g, '')   // Remove horizontal rules
    .replace(/\n{2,}/g, '\n') // Collapse multiple newlines
}