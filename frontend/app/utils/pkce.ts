/**
 * Generates a random code verifier for PKCE.
 */
export const generateCodeVerifier = (): string => {
  const array = new Uint32Array(56 / 2)
  window.crypto.getRandomValues(array)
  return Array.from(array, dec => ('0' + dec.toString(16)).substr(-2)).join('')
}

/**
 * Generates a code challenge from a code verifier using SHA-256.
 */
export const generateCodeChallenge = async (verifier: string): Promise<string> => {
  const encoder = new TextEncoder()
  const data = encoder.encode(verifier)
  const digest = await window.crypto.subtle.digest('SHA-256', data)
  return b64ue(digest)
}

/**
 * Base64url encode an ArrayBuffer.
 */
const b64ue = (buffer: ArrayBuffer): string => {
  return btoa(String.fromCharCode(...new Uint8Array(buffer)))
    .replace(/\+/g, '-')
    .replace(/\//g, '_')
    .replace(/=+$/, '')
}
