package DemoSelenium;

import org.junit.jupiter.api.Assertions;
import org.junit.jupiter.api.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;

public class RecuperacionTest extends BaseTest {

    @Test
    public void recuperar_correoRegistrado_debeContinuarProceso() {
        driver.get(Config.BASE_URL + "/recuperar");

        espera.until(ExpectedConditions.visibilityOfElementLocated(By.name("username")))
                .sendKeys("admin@cams.com");
        driver.findElement(By.cssSelector("form button")).click();

        espera.until(ExpectedConditions.visibilityOfElementLocated(
                By.xpath("//*[contains(text(),'recibiras instrucciones')]")));

        Assertions.assertTrue(driver.getPageSource().toLowerCase().contains("recibiras instrucciones"));
    }
}
