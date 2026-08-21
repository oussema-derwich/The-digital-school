export interface User {
  id: number;
  name: string;
  email: string;
  avatar: string;
}

export interface Crypto {
  id: number;
  name: string;
  symbol: string;
  icon: string;
}

export interface Transaction {
  id: number;
  user: User;
  crypto: Crypto;
  type: 'buy' | 'sell';
  quantity: number;
  amount: number;
  date: string;
}

export interface DashboardStats {
  activeUsers: string;
  userGrowth: number;
  transactionVolume: number;
  volumeChange: number;
  activeAlerts: string;
  totalTraded: number;
  tradedChange: number;
  highAlert: boolean;
}