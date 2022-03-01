<div class="box">
    <header>
        <?php if($this->showOrNull('id' , false) == null) :?>
        <h3 class="title">Registro de funcionário</h3>
        <p class="description">Formulário para registro de funcionários</p>
        <?php else:?>
            <h3 class="title">Alteração de funcionário</h3>
            <p class="description">Formulário para atualização de dados do funcionário</p>
        <?php endif;?>
    </header>
    <main>
        <form method="post" action="<?php $this->siteUrl('/funcionarios/registrar') ?>">
            <input type="hidden" name="id" value="<?php $this->showOrNull('id');?>">
            <div class="form-group">
                <input minlength="3" maxlength="150" placeholder="Nome do funcionário" class="sys-input" type="text" name="name" value="<?php $this->showOrNull('_data.name')?>">
            </div>
            <div class="form-group">
                <input minlength="3" maxlength="150" placeholder="email" class="sys-input" type="email" name="email" value="<?php $this->showOrNull('_data.email')?>">
            </div>
            <div class="form-group">
                <input minlength="11" maxlength="11" placeholder="Cpf do funcionário" class="sys-input" type="text" name="cpf" value="<?php $this->showOrNull('_data.cpf')?>">
            </div>
            <div class="form-group">
                <input minlength="3" maxlength="150" placeholder="Cargo do funcionário" class="sys-input" type="text" name="office" value="<?php $this->showOrNull('_data.office')?>">
            </div>
            <div class="action-container">
                <input class="sys-btn" type="submit" name="enviar" value="Salvar">
            </div>
        </form>
    </main>
</div>