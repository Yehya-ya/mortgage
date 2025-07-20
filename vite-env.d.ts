interface ImportMeta {
  // Vite-specific glob import
  glob<T = unknown>(pattern: string): Record<string, () => Promise<T>>;
}
