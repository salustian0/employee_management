<div class="box">
    <header>
        <?php if($this->showOrNull('id' , false) == null) :?>
        <h3 class="title">Registro de usuário</h3>
        <p class="description">Formulário para registro de usuários</p>
        <?php else:?>
            <h3 class="title">Alteração de usuário</h3>
            <p class="description">Formulário para atualização de dados do usuário</p>
        <?php endif;?>
    </header>
    <main>
        <form method="post" action="<?php $this->siteUrl('/usuarios/registrar') ?>">
            <input type="hidden" name="id" value="<?php $this->showOrNull('id');?>">
            <div class="form-group">
                <input minlength="3" maxlength="18" placeholder="Usuário" class="sys-input" type="text" name="username" value="<?php $this->showOrNull('_data.username')?>">
            </div>
            <div class="form-group">
                <input minlength="3" maxlength="18" placeholder="Senha" class="sys-input" type="password" name="password" value="<?php $this->showOrNull('_data.password')?>">
            </div>
            <div class="form-group">
                <input minlength="3" maxlength="18" placeholder="Confirmação da senha" class="sys-input" type="password" name="confirm_password" value="<?php $this->showOrNull('_data.confirm_password')?>">
            </div>
            <div class="form-group">
                <select class="sys-input" name="access">
                    <option <?php if($this->showOrNull('_data.access',false) == 'USER') {echo 'selected'; }?> value="USER">Usuário comum</option>
                    <option <?php if($this->showOrNull('_data.access',false) == 'ADM') {echo 'selected'; }?>  value="ADM">Administrador</option>
                </select>
            </div>
            <div class="action-container">
                <input class="sys-btn" type="submit" name="enviar" value="Salvar">
            </div>
        </form>
    </main>
</div>