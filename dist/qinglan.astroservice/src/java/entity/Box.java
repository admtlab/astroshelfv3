/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import javax.persistence.*;

/**
 *
 * @author panickos
 */
@Embeddable
@Access(AccessType.FIELD)
public class Box implements Serializable{

    @Embedded
    @AttributeOverrides(value={
        @AttributeOverride(name="ra", column=@Column(name="ra_bl")),
        @AttributeOverride(name="dec", column=@Column(name="dec_bl"))
    })
    private SkyPoint bottomLeft;
    
    @Embedded
    @AttributeOverrides(value={
        @AttributeOverride(name="ra", column=@Column(name="ra_tr")),
        @AttributeOverride(name="dec", column=@Column(name="dec_tr"))
    })
    private SkyPoint topRight;
    
    public Box() {
    }

    public Box(Double ra_bl, Double dec_bl, Double ra_tr, Double dec_tr) {
        this.bottomLeft = new SkyPoint(ra_bl, dec_bl);
        this.topRight = new SkyPoint(ra_tr, dec_tr);
    }

    
    public SkyPoint getBottomLeft() {
        return bottomLeft;
    }

    public void setBottomLeft(SkyPoint bottomLeft) {
        this.bottomLeft = bottomLeft;
    }

    public SkyPoint getTopRight() {
        return topRight;
    }

    public void setTopRight(SkyPoint topRight) {
        this.topRight = topRight;
    }
    
}
