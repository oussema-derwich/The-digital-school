<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Créer la table cryptocurrencies
        Schema::create('cryptocurrencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol', 10)->unique();
            $table->decimal('current_price', 18, 8)->default(0);
            $table->string('image')->nullable();
            $table->string('logo_url')->nullable();
            $table->text('description')->nullable();
            $table->boolean('in_stock')->default(true);
            $table->timestamps();
        });

        // Créer la table wallets
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->decimal('balance', 18, 2)->default(0);
            $table->string('public_address')->nullable();
            $table->string('private_address')->nullable();
            $table->timestamps();
        });

        // Créer la table wallet_cryptos (liaison entre wallets et cryptocurrencies)
        Schema::create('wallet_cryptos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
            $table->foreignId('cryptocurrency_id')->constrained('cryptocurrencies')->onDelete('cascade');
            $table->decimal('quantity', 20, 8)->default(0);
            $table->decimal('average_buy_price', 18, 2)->default(0);
            $table->unique(['wallet_id', 'cryptocurrency_id']);
            $table->timestamps();
        });

        // Créer la table transactions (reliée via wallet_crypto_id)
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_crypto_id')->constrained('wallet_cryptos')->onDelete('cascade');
            $table->enum('type', ['buy', 'sell']);
            $table->decimal('quantity', 20, 8);
            $table->decimal('unit_price', 18, 2);
            $table->decimal('total_price', 18, 2);
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->timestamps();
        });

        // Créer la table price_histories
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cryptocurrency_id')->constrained('cryptocurrencies')->onDelete('cascade');
            $table->decimal('value', 18, 8);
            $table->timestamps();
        });

        // Créer la table notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // Créer la table favorites
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cryptocurrency_id')->constrained('cryptocurrencies')->onDelete('cascade');
            $table->unique(['user_id', 'cryptocurrency_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('price_histories');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('wallet_cryptos');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('cryptocurrencies');
    }
};
