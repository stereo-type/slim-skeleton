import { Application } from "@hotwired/stimulus";
import { definitionsFromContext } from "@hotwired/stimulus-webpack-helpers";

// Создаем экземпляр приложения Stimulus
const application = Application.start();

// Изменяем настройки, например, меняем имя атрибута контроллера
application.controllerAttribute = "data-custom-controller";

// Загружаем контроллеры из другой директории, например, "js/controllers"
const context = require.context('./controllers', true, /\.js$/);
application.load(definitionsFromContext(context));
