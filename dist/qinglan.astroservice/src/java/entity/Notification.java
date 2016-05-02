/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package entity;

import java.io.Serializable;
import java.util.Collection;
import java.util.Date;
import javax.persistence.*;
import javax.validation.constraints.NotNull;
import javax.validation.constraints.Size;
import javax.xml.bind.annotation.XmlRootElement;
import org.codehaus.jackson.annotate.JsonBackReference;
import org.codehaus.jackson.annotate.JsonManagedReference;

/**
 *
 * @author panickos
 */
@Entity
@Table(name = "notification")
@XmlRootElement
@NamedQueries({
    @NamedQuery(name = "Notification.findAll", query = "SELECT n FROM Notification n"),
    @NamedQuery(name = "Notification.findById", query = "SELECT n FROM Notification n WHERE n.id = :id"),
    @NamedQuery(name = "Notification.findByTs", query = "SELECT n FROM Notification n WHERE n.ts = :ts"),
    @NamedQuery(name = "Notification.findByIsRead", query = "SELECT n FROM Notification n WHERE n.isRead = :isRead"),
    @NamedQuery(name = "Notification.findByStarred", query = "SELECT n FROM Notification n WHERE n.starred = :starred"),
    @NamedQuery(name = "Notification.findByNotificationData", query = "SELECT n FROM Notification n WHERE n.notificationData = :notificationData")})
public class Notification implements Serializable {
    private static final long serialVersionUID = 1L;
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    @Basic(optional = false)
    @NotNull
    @Column(name = "id")
    private Long id;
    @Basic(optional = false)
    @NotNull
    @Column(name = "ts")
    @Temporal(TemporalType.TIMESTAMP)
    private Date ts;
    @Basic(optional = false)
    @NotNull
    @Column(name = "is_read")
    private short isRead;
    @Basic(optional = false)
    @NotNull
    @Column(name = "starred")
    private short starred;
    @Basic(optional = false)
    @NotNull
    @Size(min = 1, max = 2000)
    @Column(name = "notification_data")
    private String notificationData;
    @ManyToMany(mappedBy = "notificationCollection")
    private Collection<Liveinterest> liveinterestCollection;
    @JoinColumn(name = "user_id", referencedColumnName = "user_id")
    @ManyToOne(optional = false)
    private User userId;

    public Notification() {
    }

    public Notification(Long id) {
        this.id = id;
    }

    public Notification(Long id, Date ts, short isRead, short starred, String notificationData) {
        this.id = id;
        this.ts = ts;
        this.isRead = isRead;
        this.starred = starred;
        this.notificationData = notificationData;
    }

    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public Date getTs() {
        return ts;
    }

    public void setTs(Date ts) {
        this.ts = ts;
    }

    public short getIsRead() {
        return isRead;
    }

    public void setIsRead(short isRead) {
        this.isRead = isRead;
    }

    public short getStarred() {
        return starred;
    }

    public void setStarred(short starred) {
        this.starred = starred;
    }

    public String getNotificationData() {
        return notificationData;
    }

    public void setNotificationData(String notificationData) {
        this.notificationData = notificationData;
    }

//    @XmlTransient
//    @JsonIgnore
    //@JsonManagedReference("liveinterest-notification")
    public Collection<Liveinterest> getLiveinterestCollection() {
        return liveinterestCollection;
    }

    public void setLiveinterestCollection(Collection<Liveinterest> liveinterestCollection) {
        this.liveinterestCollection = liveinterestCollection;
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
        hash += (id != null ? id.hashCode() : 0);
        return hash;
    }

    @Override
    public boolean equals(Object object) {
        // TODO: Warning - this method won't work in the case the id fields are not set
        if (!(object instanceof Notification)) {
            return false;
        }
        Notification other = (Notification) object;
        if ((this.id == null && other.id != null) || (this.id != null && !this.id.equals(other.id))) {
            return false;
        }
        return true;
    }

    @Override
    public String toString() {
        return "entity.Notification[ id=" + id + " ]";
    }
    
}
