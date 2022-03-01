<div class="box">
    <header>
            <h3 class="title">Login</h3>
            <p class="description">Realize seu login na plataforma.</p>
    </header>
    <main>
        <form method="post" action="<?php $this->siteUrl('/autenticar') ?>">
            <div class="form-group">
                <input minlength="3" maxlength="18" placeholder="Usuário" class="sys-input" type="text" name="username" value="<?php $this->showOrNull('_data.username')?>">
            </div>
            <div class="form-group">
                <input minlength="3" maxlength="18" placeholder="Senha" class="sys-input" type="password" name="password" value="<?php $this->showOrNull('_data.password')?>">
            </div>
            <div class="action-container">
                <input class="sys-btn" type="submit" name="enviar" value="Logar">
            </div>
        </form>
    </main>
</div>