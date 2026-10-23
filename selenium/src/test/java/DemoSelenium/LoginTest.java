package DemoSelenium;

import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;

public class LoginTest extends BaseTest {

    @Test
    public void acceso_credencialesCorrectas_debePedirCodigo() {
        driver.findElement(By.name("username")).sendKeys("almacen@cams.com");
        driver.findElement(By.name("password")).sendKeys("cams2026");
        driver.findElement(By.cssSelector("button[type='submit']")).click();

        int cajas = espera.until(
                ExpectedConditions.numberOfElementsToBe(By.cssSelector(".otp-digit"), 6)).size();

        Assertions.assertEquals(6, cajas);
    }

    @Test
    public void acceso_credencialesIncorrectas_debeMostrarMensaje() {
        driver.findElement(By.name("username")).sendKeys("almacen@cams.com");
        driver.findElement(By.name("password")).sendKeys("clave-mala");
        driver.findElement(By.cssSelector("button[type='submit']")).click();

        WebElement mensaje = espera.until(
                ExpectedConditions.visibilityOfElementLocated(By.cssSelector(".message.error")));

        Assertions.assertTrue(mensaje.getText().toLowerCase().contains("no son validas"));
    }

    @Test
    public void acceso_credencialesVacias_debeMostrarError() {
        ((JavascriptExecutor) driver).executeScript(
                "document.querySelector('form[data-step=\\'login\\']').submit();");

        WebElement mensaje = espera.until(
                ExpectedConditions.visibilityOfElementLocated(By.cssSelector(".message.error")));

        Assertions.assertFalse(mensaje.getText().isEmpty());
    }
}
