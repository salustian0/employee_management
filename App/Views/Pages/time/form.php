<div class="box">
    <header>
        <?php if($this->showOrNull('id' , false) == null) :?>
        <h3 class="title">Registro de ponto</h3>
        <p class="description">Formulário para registro de ponto do funcionários</p>
        <?php else:?>
            <h3 class="title">Alteração de ponto</h3>
            <p class="description">Formulário para atualização de ponto do funcionário</p>
        <?php endif;?>
    </header>
    <main>
        <form method="post" action="<?php $this->siteUrl('/ponto/registrar') ?>">
            <input type="hidden" name="id" value="<?php $this->showOrNull('id');?>">
            <div class="form-group">
                <input placeholder="Horá inicial" class="sys-input" type="time" name="start" value="<?php $this->showOrNull('_data.start')?>">
            </div>
            <div class="form-group">
                <input placeholder="Horá final" class="sys-input" type="time" name="end" value="<?php $this->showOrNull('_data.end')?>">
            </div>
            <div class="form-group">
                <input placeholder="Data" class="sys-input" type="date" name="date" value="<?php $this->showOrNull('_data.date')?>">
            </div>

            <div class="form-group">
                <select class="sys-input" name="employee_id">
                <?php if(!empty($list_employees)):?>
                    <?php foreach ($list_employees as $employeeEntity):?>
                    <option value="<?php echo $employeeEntity->getId()?>"><?php echo \App\system\Utils\Security::securityString($employeeEntity->getName())?></option>
                    <?php endforeach;?>
                <?php endif;?>
                </select>
            </div>

            <div class="action-container">
                <input class="sys-btn" type="submit" name="enviar" value="Salvar">
            </div>
        </form>
    </main>
</div>