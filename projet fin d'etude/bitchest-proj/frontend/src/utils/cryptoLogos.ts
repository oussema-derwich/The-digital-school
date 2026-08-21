/**
 * Crypto Logo Utils
 * Maps cryptocurrencies to their logo image paths
 */

export const cryptoLogos: Record<string, string> = {
  'bitcoin': '/assets/bitcoin.png',
  'btc': '/assets/bitcoin.png',
  'ethereum': '/assets/ethereum.png',
  'eth': '/assets/ethereum.png',
  'cardano': '/assets/cardano.png',
  'ada': '/assets/cardano.png',
  'solana': '/assets/stellar.png',
  'sol': '/assets/stellar.png',
  'ripple': '/assets/ripple.png',
  'xrp': '/assets/ripple.png',
  'litecoin': '/assets/litecoin.png',
  'ltc': '/assets/litecoin.png',
  'bitcoin cash': '/assets/bitcoin-cash.png',
  'bch': '/assets/bitcoin-cash.png',
  'dash': '/assets/dash.png',
  'iota': '/assets/iota.png',
  'nem': '/assets/nem.png',
  'xem': '/assets/nem.png'
}

/**
 * Retourne le chemin du logo d'une cryptomonnaie
 * @param name Nom ou symbole de la crypto
 * @returns URL du logo
 */
export const getCryptoLogo = (logoKey: string): string => {
  return cryptoLogos[logoKey.toLowerCase()] || '/assets/bitcoin.png'
}

/**
 * Retourne les classes Tailwind pour le style du logo
 */
export const getLogoClasses = (logoKey: string): string => {
  const classes: Record<string, string> = {
    bitcoin: 'from-orange-400 to-orange-600',
    ethereum: 'from-purple-500 to-purple-700',
    cardano: 'from-blue-500 to-blue-700',
    solana: 'from-cyan-400 to-cyan-600',
    btc: 'from-orange-400 to-orange-600',
    eth: 'from-purple-500 to-purple-700',
    ada: 'from-blue-500 to-blue-700',
    sol: 'from-cyan-400 to-cyan-600'
  }
  return classes[logoKey.toLowerCase()] || 'from-gray-400 to-gray-600'
}

/**
 * Retourne la couleur du texte pour le logo
 */
export const getCryptoColor = (logoKey: string): string => {
  const colors: Record<string, string> = {
    bitcoin: 'text-orange-500',
    ethereum: 'text-purple-600',
    cardano: 'text-blue-600',
    solana: 'text-cyan-500',
    btc: 'text-orange-500',
    eth: 'text-purple-600',
    ada: 'text-blue-600',
    sol: 'text-cyan-500'
  }
  return colors[logoKey.toLowerCase()] || 'text-gray-500'
}

/**
 * Retourne les données des cryptos avec leurs logos réels
 */
export const getCryptoData = () => [
  {
    id: 1,
    name: 'Bitcoin',
    symbol: 'BTC',
    logo: getCryptoLogo('Bitcoin'),
    price: '82 250',
    variation: '+3.72',
    volume: '120M'
  },
  {
    id: 2,
    name: 'Ethereum',
    symbol: 'ETH',
    logo: getCryptoLogo('Ethereum'),
    price: '6 950',
    variation: '-1.12',
    volume: '85M'
  },
  {
    id: 3,
    name: 'Cardano',
    symbol: 'ADA',
    logo: getCryptoLogo('Cardano'),
    price: '1 250',
    variation: '+2.45',
    volume: '45M'
  },
  {
    id: 4,
    name: 'Solana',
    symbol: 'SOL',
    logo: getCryptoLogo('Solana'),
    price: '180',
    variation: '+1.80',
    volume: '32M'
  },
  {
    id: 5,
    name: 'Ripple',
    symbol: 'XRP',
    logo: getCryptoLogo('Ripple'),
    price: '2 100',
    variation: '-0.50',
    volume: '28M'
  },
  {
    id: 6,
    name: 'Litecoin',
    symbol: 'LTC',
    logo: getCryptoLogo('Litecoin'),
    price: '12 500',
    variation: '+1.25',
    volume: '18M'
  }
]
