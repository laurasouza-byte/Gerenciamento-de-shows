-- Banco de dados do projeto Gerenciador de Shows
-- Sintaxe PostgreSQL (Supabase)
-- Rode isto direto no "SQL Editor" do painel do Supabase

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE shows (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    artista VARCHAR(150),
    venue VARCHAR(150) NOT NULL,
    show_date TIMESTAMP NOT NULL,
    capacity INT NOT NULL,
    ticket_price NUMERIC(10,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    id_user INT NOT NULL REFERENCES users(id)
);
