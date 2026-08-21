#!/usr/bin/env node

/**
 * Test script to verify auth endpoints
 * Run: node backend/test_auth_endpoints.js
 */

import axios from 'axios';

const API_BASE = 'http://localhost:8000/api';

// Test credentials
const testUser = {
  email: 'admin@example.com',
  password: 'password'
};

async function testHealth() {
  try {
    console.log('\n📌 Testing Health Endpoint...');
    const response = await axios.get(`${API_BASE}/health`);
    console.log('✅ Health check passed:', response.data);
    return true;
  } catch (error) {
    console.error('❌ Health check failed:', error.message);
    return false;
  }
}

async function testLogin() {
  try {
    console.log('\n📌 Testing Login Endpoint...');
    console.log('📧 Testing with email:', testUser.email);
    
    const response = await axios.post(`${API_BASE}/auth/login`, {
      email: testUser.email,
      password: testUser.password
    });

    console.log('✅ Login successful');
    console.log('Response structure:', Object.keys(response.data));
    
    // Check response structure
    const data = response.data;
    console.log('- status:', data.status);
    console.log('- message:', data.message);
    console.log('- user exists:', !!data.user);
    console.log('- token exists:', !!data.token);
    console.log('- data.user exists:', !!data.data?.user);
    console.log('- data.token exists:', !!data.data?.token);

    if (data.user) {
      console.log('\n👤 User info:');
      console.log('  - id:', data.user.id);
      console.log('  - name:', data.user.name);
      console.log('  - email:', data.user.email);
      console.log('  - role:', data.user.role);
      console.log('  - avatar:', data.user.avatar ? '✓' : 'none');
    }

    if (data.data?.user) {
      console.log('\n👤 User info (from data.data):');
      console.log('  - id:', data.data.user.id);
      console.log('  - name:', data.data.user.name);
      console.log('  - email:', data.data.user.email);
      console.log('  - role:', data.data.user.role);
    }

    if (data.token) {
      console.log('\n🔐 Token:', data.token.substring(0, 20) + '...');
    }

    return response.data.token || data.data?.token;
  } catch (error) {
    console.error('❌ Login failed:', error.response?.data || error.message);
    return null;
  }
}

async function testLogout(token) {
  try {
    console.log('\n📌 Testing Logout Endpoint...');
    
    const response = await axios.post(`${API_BASE}/auth/logout`, {}, {
      headers: { Authorization: `Bearer ${token}` }
    });

    console.log('✅ Logout successful:', response.data);
    return true;
  } catch (error) {
    console.error('❌ Logout failed:', error.response?.data || error.message);
    return false;
  }
}

async function testProfile(token) {
  try {
    console.log('\n📌 Testing Profile Endpoint...');
    
    const response = await axios.get(`${API_BASE}/auth/profile`, {
      headers: { Authorization: `Bearer ${token}` }
    });

    console.log('✅ Profile fetch successful');
    console.log('Response structure:', Object.keys(response.data));
    
    const userData = response.data.data || response.data.user;
    if (userData) {
      console.log('\n👤 Profile info:');
      console.log('  - id:', userData.id);
      console.log('  - name:', userData.name);
      console.log('  - email:', userData.email);
      console.log('  - role:', userData.role);
    }

    return true;
  } catch (error) {
    console.error('❌ Profile fetch failed:', error.response?.data || error.message);
    return false;
  }
}

async function runTests() {
  console.log('🧪 BitChest Auth Endpoints Test Suite');
  console.log('=====================================\n');
  console.log('API Base URL:', API_BASE);

  // Test health
  const healthOk = await testHealth();
  if (!healthOk) {
    console.error('\n⚠️  API is not accessible. Make sure the backend is running.');
    process.exit(1);
  }

  // Test login
  const token = await testLogin();
  if (!token) {
    console.error('\n⚠️  Login failed. Check your credentials.');
    process.exit(1);
  }

  // Test profile
  await testProfile(token);

  // Test logout
  await testLogout(token);

  console.log('\n✅ All tests completed!\n');
}

// Run tests
runTests().catch(error => {
  console.error('Test suite failed:', error);
  process.exit(1);
});
