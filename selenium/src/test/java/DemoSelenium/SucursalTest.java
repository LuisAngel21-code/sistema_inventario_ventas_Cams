package DemoSelenium;

import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;

public class SucursalTest extends BaseTest {

    @Test
    public void registrar_sucursal_debeAparecerEnListado() {
        iniciarSesion("admin@cams.com", "cams2026");

        String nombre = "Sucursal Selenium " + System.currentTimeMillis();

        driver.get(Config.BASE_URL + "/sucursales/crear");
        espera.until(ExpectedConditions.visibilityOfElementLocated(By.name("nombre"))).sendKeys(nombre);
        driver.findElement(By.name("direccion")).sendKeys("Av. Selenium 123");
        driver.findElement(By.name("telefono")).sendKeys("999888777");
        driver.findElement(By.cssSelector("main form button")).click();

        esperarTexto("Sucursal registrada correctamente");

        Assertions.assertTrue(driver.getPageSource().contains(nombre));
    }
}
