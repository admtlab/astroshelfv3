/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import java.util.Collection;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;
import javax.xml.bind.annotation.XmlTransient;
import org.codehaus.jackson.annotate.JsonBackReference;
import org.codehaus.jackson.annotate.JsonIgnore;
import org.codehaus.jackson.annotate.JsonManagedReference;

/**
 *
 * @author panickos
 */
@Entity
@Table(name = "liveinterest")
@Access(AccessType.FIELD)
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "Liveinterest.findAll", query = "SELECT l FROM Liveinterest l"),
    @NamedQuery(name = "Liveinterest.findByInterestId", query = "SELECT l FROM Liveinterest l WHERE l.interestId = :interestId"),
    @NamedQuery(name = "Liveinterest.findByLabel", query = "SELECT l FROM Liveinterest l WHERE l.label = :label"),
    @NamedQuery(name = "Liveinterest.findByKeyword", query = "SELECT l FROM Liveinterest l WHERE l.keyword = :keyword"),
    @NamedQuery(name = "Liveinterest.findByRaBl", query = "SELECT l FROM Liveinterest l WHERE l.box.bottomLeft.ra = :raBl"),
    @NamedQuery(name = "Liveinterest.findByDecBl", query = "SELECT l FROM Liveinterest l WHERE l.box.bottomLeft.dec = :decBl"),
    @NamedQuery(name = "Liveinterest.findByRaTr", query = "SELECT l FROM Liveinterest l WHERE l.box.topRight.ra = :raTr"),
    @NamedQuery(name = "Liveinterest.findByDecTr", query = "SELECT l FROM Liveinterest l WHERE l.box.topRight.dec = :decTr"),
    @NamedQuery(name = "Liveinterest.findByActive", query = "SELECT l FROM Liveinterest l WHERE l.active = :active"),
    @NamedQuery(name = "Liveinterest.findNotificationsByInterestId", query = "SELECT n FROM Liveinterest i JOIN i.notificationCollection n WHERE n.isRead = :isRead AND i.interestId = :interestId")})
public class Liveinterest implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    //@NotNull
    @Column(name = "interest_id")
    private Long interestId;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 100)
    @Column(name = "label")
    private String label;
    @Size(max = 1000)
    @Column(name = "keyword")
    private String keyword;
    
    @Embedded
    private Box box;

    @Basic(optional = false)
    @NotNull
    @Column(name = "active")
    private short active;
    @JoinTable(name = "notification_to_liveinterest", joinColumns = {
        @JoinColumn(name = "interest_id", referencedColumnName = "interest_id")}, inverseJoinColumns = {
        @JoinColumn(name = "notification_id", referencedColumnName = "id")})
    @ManyToMany
    private Collection<Notification> notificationCollection;
    @JoinColumn(name = "user_id", referencedColumnName = "user_id")
    @ManyToOne(optional = false)
    private User userId;

    public Liveinterest() {
    }

    public Liveinterest(Long interestId) {
        this.interestId = interestId;
    }

    public Liveinterest(Long interestId, String label, double raBl, double decBl, double raTr, double decTr, short active) {
        this.interestId = interestId;
        this.label = label;
        this.box = new Box(raBl, decBl, raTr, decTr);
        this.active = active;
    }

    public Long getInterestId() {
        return interestId;
    }

    public void setInterestId(Long interestId) {
        this.interestId = interestId;
    }

    public String getLabel() {
        return label;
    }

    public void setLabel(String label) {
        this.label = label;
    }

    public String getKeyword() {
        return keyword;
    }

    public void setKeyword(String keyword) {
        this.keyword = keyword;
    }

    public Box getBox() {
        return box;
    }

    public void setBox(Box box) {
        this.box = box;
    }
    
    public short getActive() {
        return active;
    }

    public void setActive(short active) {
        this.active = active;
    }

    @XmlTransient
    @JsonIgnore
    //@JsonBackReference("liveinterest-notification")
    public Collection<Notification> getNotificationCollection() {
        return notificationCollection;
    }

    public void setNotificationCollection(Collection<Notification> notificationCollection) {
        this.notificationCollection = notificationCollection;
    }

    public User getUserId() {
        return userId;
    }

    public void setUserId(User userId) {
        this.userId = userId;
    }

    @Override
    public int hashCode() {
        int hash = 0;
        hash += (interestId != null ? interestId.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof Liveinterest)) {
            return false;
        }
        Liveinterest other = (Liveinterest) object;
        if ((this.interestId == null && other.interestId != null) || (this.interestId != null && !this.interestId.equals(other.interestId))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.Liveinterest[ interestId=" + interestId + " ]";
    }
    
}
