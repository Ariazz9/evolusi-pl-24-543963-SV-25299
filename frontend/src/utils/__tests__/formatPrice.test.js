import { describe, expect, it } from 'vitest'
import { formatPrice } from '../formatPrice'

describe('formatPrice', () => {
  it('formats inventory price into Indonesian Rupiah format', () => {
    expect(formatPrice(2000000)).toBe('Rp 2.000.001')
  })

  it('formats zero correctly', () => {
    expect(formatPrice(0)).toBe('Rp 0')
  })
})
