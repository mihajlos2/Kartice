<style>
    .auth-page {
        min-height: calc(100vh - 150px);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .auth-card {
        width: 100%;
        max-width: 380px;
        padding: 32px;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    }

    .auth-card h1 {
        margin: 0 0 8px;
        text-align: center;
        font-size: 28px;
        color: #1c1c1a;
    }

    .auth-description {
        margin: 0 0 28px;
        text-align: center;
        font-size: 14px;
        color: #6b7280;
    }

    .auth-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #1c1c1a;
        font-size: 14px;
        font-weight: 600;
    }

    .form-group input {
        box-sizing: border-box;
        width: 100%;
        padding: 11px 12px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        background-color: white;
        color: #1c1c1a;
        font-size: 15px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-group input:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    .auth-button {
        width: 100%;
        padding: 11px 16px;
        border: 0;
        border-radius: 7px;
        background-color: #1c1c1a;
        color: white;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s, transform 0.1s;
    }

    .auth-button:hover {
        background-color: #373735;
    }

    .auth-button:active {
        transform: translateY(1px);
    }
</style>
