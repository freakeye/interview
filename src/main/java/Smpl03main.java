// package onlocal.testing.selenium

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.firefox.FirefoxDriver;
import org.openqa.selenium.firefox.MarionetteDriver;
import org.openqa.selenium.remote.DesiredCapabilities;
import org.openqa.selenium.support.ui.ExpectedCondition;
import org.openqa.selenium.support.ui.WebDriverWait;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileWriter;
import java.io.IOException;


public class Smpl03main {

    private static final String DEFAULT_SEARCH_PHRASE = new String("qa");
    private static final char SEARCH_PHRASE_DELIMETER = ' ';

    private static final String marionetteDriverLocation =
            "D:\\Selenium\\geckodriver0.9\\geckodriver.exe\\";
    private static String searchPhrase = DEFAULT_SEARCH_PHRASE;

    private static String cssLocator = new String("#rso > div > div:nth-child"
            + "(3) > div > h3 > a");

    private static WebDriver driver;

// http://automated-testing.info/t/topic/1741/9
    public static void switchWindow(WebDriver driver, int numberOfWindow) {
        String handle = driver.getWindowHandles().toArray()[numberOfWindow].toString();
        driver.switchTo().window(handle);
    }


    public static String openInNewWindow(String url) {
        String name = "some_random_name";
        ((JavascriptExecutor) driver)
                .executeScript("window.open(arguments[0], \"" + name + "\")", url);
        return name;
    }

    public static void main(String[] args)
//            throws IOException
//            throws InterruptedException
    {
        System.setProperty("webdriver.gecko.driver", marionetteDriverLocation);
        DesiredCapabilities capabilities = DesiredCapabilities.firefox();
        capabilities.setCapability("marionette", true);
        driver = new FirefoxDriver(capabilities);

        driver.get("https://www.google.ru");
        WebElement element = driver.findElement(By.name("q"));

        if (args.length > 0) {
            // parsing arguments
            StringBuilder srchPhraseSb = new StringBuilder();
            for (String s : args) {
                srchPhraseSb.append(s);
                srchPhraseSb.append(SEARCH_PHRASE_DELIMETER);
            }
            searchPhrase = srchPhraseSb.toString();
        }

        element.sendKeys(searchPhrase);
        element.submit();

        (new WebDriverWait(driver, 10)).until(new ExpectedCondition<Boolean>() {
            public Boolean apply(WebDriver d) {
                return d.findElement(By.cssSelector(cssLocator)).isDisplayed();
            }
        });

        WebElement srchEl;
        srchEl = driver.findElement(By.cssSelector(cssLocator));
// System.out.println(srchEl);
        srchEl.click();
//        driver.navigate().forward();
//        WebWindow newBrowserTab =
        String srchHref = srchEl.getAttribute("href");
System.out.println(srchHref);

//        driver.get(srchHref);
//        ((JavascriptExecutor) driver).executeScript("window.open(arguments[0])", srchHref);
//        switchWindow(driver, 1);
        driver.switchTo().window( openInNewWindow(srchHref) );



        String htmlContentAsString = driver.getPageSource();
System.out.println(htmlContentAsString);


/*
        File outFile = new File("out.html");
        BufferedWriter outFileW;
        try {
            if (!outFile.exists()) {
                outFile.createNewFile();
            }
            outFileW = new BufferedWriter(new FileWriter(outFile.getAbsoluteFile()));

            try {
                outFileW.write(htmlContentAsString);
            } finally {
                outFileW.close();
            }
        } catch (IOException e) {
            e.printStackTrace();
        }

*/


        try {
            Thread.sleep(2000);
        } catch (InterruptedException e) {
            e.printStackTrace();
        }

        driver.quit();
    }
}
